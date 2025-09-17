<?php
declare(strict_types=1);

class CfmApiClient
{
    private const API_BASE_URL = 'https://portal.cfm.org.br/api_rest_php/api/v1/medicos/';

    public function buscarMedicos(array $filtros = [], int $pagina = 1): ?array
    {
        $payload = [
            [
                'medico' => array_merge([
                    'nome' => '',
                    'ufMedico' => $_POST['uf'],
                    'crmMedico' => $_POST['identificador'],
                ], $filtros),
                'page' => $pagina,
                'pageSize' => 1,
            ]
        ];
    

        echo "Payload: ";
        echo "<pre>";
        print_r($payload);
        echo "</pre>";




        return $this->fazerRequisicao('buscar_medicos', $payload);
    }

    private function fazerRequisicao(string $endpoint, array $payload): ?array
    {
        $url = self::API_BASE_URL . $endpoint;
        $ch = curl_init($url);
        
        try {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Apenas para testes locais
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Apenas para testes locais
            
            $response = curl_exec($ch);
            
            if ($response === false) {
                throw new Exception('Erro na requisição cURL: ' . curl_error($ch));
            }
            
            return json_decode($response, true);
        } finally {
            curl_close($ch);
        }
    }

}