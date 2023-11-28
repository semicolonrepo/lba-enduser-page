<?php

namespace App\Http\Controllers;

use App\Models\CustomerUser;
use App\Services\CampaignService;
use App\Services\OtpService;
use Illuminate\Http\Request;

class OneTimePasswordController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private OtpService $otpService,
    ) {}

    public function login($brand, $campaign, $productId) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        switch($campaignData->page_template_id) {
            case 1:
                return view('lba-1.auth.phone_number', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ]);
            case 2:
                return view('lba-2.auth.phone_number', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ]);
            default:
                return view('welcome_custom', ['message' => 'Campaign not found.']);
        }
    }

    public function send(Request $request, $brand, $campaign, $productId) {
        $request->validate([
            'phone_number' => 'required',
        ]);
        $phoneNumber = $request->input('phone_number');

        if (strpos($phoneNumber, '0') === 0) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        try {
            $otpType = $this->otpService->getOtpTypeByCode('WA_GATEWAY_ZENZIVA');
            $sendOpt = $this->otpService->sendOtp($otpType->id, $phoneNumber);

            if ($sendOpt) {
                return redirect()->route('otp::validate::get', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'phoneNumber' => $phoneNumber,
                ]);
            }

            return redirect()->back()->with('failed', 'Otp mengalami gangguan silahkan coba kembali!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function validateGet($brand, $campaign, $productId, $phoneNumber) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        if (strpos($phoneNumber, '0') === 0) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        switch($campaignData->page_template_id) {
            case 1:
                return view('lba-1.auth.otp', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'phoneNumber' => $phoneNumber,
                ]);
            case 2:
                return view('lba-2.auth.otp', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'phoneNumber' => $phoneNumber,
                ]);
            default:
                return view('welcome_custom', ['message' => 'Campaign not found.']);
        }
    }

    public function validatePost(Request $request, $brand, $campaign, $productId, $phoneNumber) {
        $request->validate([
            'otp_number' => 'required',
        ]);

        $otpNumber = $request->input('otp_number');

        if (strpos($phoneNumber, '0') === 0) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        try {

            $otpType = $this->otpService->getOtpTypeByCode('WA_GATEWAY_ZENZIVA');
            $validateOtp = $this->otpService->validateOtp($otpType->id, $phoneNumber, $otpNumber);

            if ($validateOtp) {
                return redirect()->route('voucher::claim', [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ]);
            }

            return redirect()->back()->with('failed', 'Otp salah silahkan coba kembali!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'terjadi kesalahan');
        }
    }
}
