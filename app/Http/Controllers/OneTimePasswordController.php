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

        $viewTemplate = $campaignData->page_template_code . '.auth.phone_number';
        return view($viewTemplate, [
            'brand' => $brand,
            'campaign' => $campaign,
            'productId' => $productId,
            'data' => $campaignData,
        ]);
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

        $viewTemplate = $campaignData->page_template_code . '.auth.otp';
        return view($viewTemplate, [
            'brand' => $brand,
            'campaign' => $campaign,
            'productId' => $productId,
            'phoneNumber' => $phoneNumber,
            'data' => $campaignData,
        ]);
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
