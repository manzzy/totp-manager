<?php

namespace ugw\TOTPManager;

use OTPHP\TOTP;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class TOTPManager
{
    protected $issuer;

    public function __construct(string $issuer = 'Proto Dashboard')
    {
        $this->issuer = $issuer;
    }

    public function generateSecret(string $userLabel): array
    {
        $totp = TOTP::create();
        $totp->setLabel($userLabel);
        $totp->setIssuer($this->issuer);

        $secret = $totp->getSecret();
        $qrUrl = $totp->getProvisioningUri();

        $qrImage = Builder::create()
            ->writer(new PngWriter())
            ->data($qrUrl)
            ->size(200)
            ->build()
            ->getDataUri();

        return [
            'secret' => $secret,
            'qr_code' => $qrImage,
            'provisioning_uri' => $qrUrl,
        ];
    }

    public function verifyCode(string $secret, string $userCode): bool
    {
        $totp = TOTP::create($secret);
        $totp->setIssuer($this->issuer);

        return $totp->verify($userCode);
    }
}
