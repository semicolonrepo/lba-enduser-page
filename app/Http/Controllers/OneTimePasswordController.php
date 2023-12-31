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
            'phone_number' => 'required|numeric',
        ]);
        $phoneNumber = $request->input('phone_number');
        $partnerId = $request->input('partner');

        $phoneNumber = str_replace('+', '', $phoneNumber);
        $phoneNumber = str_replace('-', '', $phoneNumber);
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (strpos($phoneNumber, '0') === 0) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        try {
            if ($partnerId) {
                session(['partner_id' => $partnerId]);
            }

            $otpType = $this->otpService->getOtpTypeByCode('WA_GATEWAY_ZENZIVA');
            $sendOpt = $this->otpService->sendOtp($phoneNumber);

            if ($sendOpt) {
                $utmSource = $request->query('utm_source');

                if($utmSource) {
                    return redirect()->route('otp::validate::get', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'productId' => $productId,
                        'phoneNumber' => $phoneNumber,
                        'utm_source' => $utmSource
                    ]);
                }
                else {
                    return redirect()->route('otp::validate::get', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'productId' => $productId,
                        'phoneNumber' => $phoneNumber,
                    ]);
                }
            }

            return redirect()->back()->with('failed', 'Otp mengalami gangguan silahkan coba kembali!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function resend($brand, $campaign, $productId, $phoneNumber) {
        try {
            $otpType = $this->otpService->getOtpTypeByCode('WA_GATEWAY_ZENZIVA');
            $sendOpt = $this->otpService->sendOtp($phoneNumber);

            if ($sendOpt) {
                return redirect()->back()->with('success', 'Otp berhasil dikirim ulang!');
            }

            return redirect()->back()->with('failed', 'Otp mengalami gangguan silahkan coba kembali!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function validateGet($brand, $campaign, $productId, $phoneNumber) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        $phoneNumber = str_replace('+', '', $phoneNumber);
        $phoneNumber = str_replace('-', '', $phoneNumber);
        $phoneNumber = str_replace(' ', '', $phoneNumber);
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
            'otp_number' => 'required|numeric',
        ]);

        $otpNumber = $request->input('otp_number');

        $phoneNumber = str_replace('+', '', $phoneNumber);
        $phoneNumber = str_replace('-', '', $phoneNumber);
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (strpos($phoneNumber, '0') === 0) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        try {
            $otpType = $this->otpService->getOtpTypeByCode('WA_GATEWAY_ZENZIVA');
            $validateOtp = $this->otpService->validateOtp($otpType->id, $phoneNumber, $otpNumber);

            if ($validateOtp) {
                $utmSource = $request->query('utm_source');

                if($utmSource) {
                    return redirect()->route('voucher::claim', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'productId' => $productId,
                        'utm_source' => $utmSource
                    ]);
                }
                else {
                    return redirect()->route('voucher::claim', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'productId' => $productId,
                    ]);
                }
            }

            return redirect()->back()->with('failed', 'Otp salah silahkan coba kembali!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'terjadi kesalahan');
        }
    }
}
