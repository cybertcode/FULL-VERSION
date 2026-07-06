<?php

namespace App\Services\System;

class DeveloperCreditService
{
    private const KEY = 'Vx7kQ9mZ';

    private const NAME_ENCODED = 'Eh1BDj1WHQ4zG18=';

    private const URL_ENCODED = 'PgxDGyIDQnUhD0BFPVADMTMcXgV/WgI3eRFZRDxSBTJ5';

    public function name(): string
    {
        return $this->decode(self::NAME_ENCODED);
    }

    public function url(): string
    {
        return $this->decode(self::URL_ENCODED);
    }

    public function signature(): string
    {
        return hash('sha256', $this->name().'|'.$this->url());
    }

    /**
     * Compara el hash de una firma recibida (ej. de la vista renderizada)
     * contra la firma esperada, para detectar si el crédito fue alterado.
     */
    public function verify(?string $renderedSignature): bool
    {
        return $renderedSignature !== null && hash_equals($this->signature(), $renderedSignature);
    }

    private function decode(string $encoded): string
    {
        $data = base64_decode($encoded);
        $key = self::KEY;
        $out = '';

        for ($i = 0; $i < \strlen($data); $i++) {
            $out .= $data[$i] ^ $key[$i % \strlen($key)];
        }

        return $out;
    }
}
