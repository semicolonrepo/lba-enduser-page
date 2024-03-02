<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\OtpService;
use Illuminate\Http\Request;

class OneTimePasswordRatingController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private OtpService $otpService,
    ) {}

    public function login($brand, $campaign, $voucherCode) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        $viewTemplate = $campaignData->page_template_code . '.auth.phone_number';
        return view($viewTemplate, [
            'brand' => $brand,
            'campaign' => $campaign,
            'voucherCode' => $voucherCode,
            'data' => $campaignData,
        ]);
    }

    public function send(Request $request, $brand, $campaign, $voucherCode) {
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
                    return redirect()->route('otp::validate::get::rating', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'voucherCode' => $voucherCode,
                        'phoneNumber' => $phoneNumber,
                        'utm_source' => $utmSource
                    ]);
                }
                else {
                    return redirect()->route('otp::validate::get::rating', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'voucherCode' => $voucherCode,
                        'phoneNumber' => $phoneNumber,
                    ]);
                }
            }

            return redirect()->back()->with('failed', 'Otp mengalami gangguan silahkan coba kembali!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function resend($brand, $campaign, $voucherCode, $phoneNumber) {
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

    public function validateGet($brand, $campaign, $voucherCode, $phoneNumber) {
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
            'voucherCode' => $voucherCode,
            'phoneNumber' => $phoneNumber,
            'data' => $campaignData,
        ]);
    }

    public function validatePost(Request $request, $brand, $campaign, $voucherCode, $phoneNumber) {
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
                    return redirect()->route('rating::show', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'voucherCode' => $voucherCode,
                        'utm_source' => $utmSource
                    ]);
                }
                else {
                    return redirect()->route('rating::show', [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'voucherCode' => $voucherCode,
                    ]);
                }
            }

            return redirect()->back()->with('failed', 'Otp salah silahkan coba kembali!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'terjadi kesalahan');
        }
    }
}
