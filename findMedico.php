<?php



declare(strict_types=1);
echo '<pre>';
print_r($_POST);
echo '</pre>';

/**
 * Fetches doctor data from the API.
 *
 * @return array|null The decoded JSON response or null on error.
 */
function fetchDoctors(): ?array
{
    $url = 'https://portal.cfm.org.br/api_rest_php/api/v1/medicos/buscar_medicos';
    $uf = 'PE'; // Example UF, you can change this as needed
    $crm = '13386'; // Example CRM, you can change this as needed
    $data = [
        [
            'medico' => [
                'nome' => '',
                'ufMedico' =>$uf,
                'crmMedico' => $crm,
                'municipioMedico' => '',
                'tipoInscricaoMedico' => '',
                'situacaoMedico' => '',
                'detalheSituacaoMedico' => '',
                'especialidadeMedico' => '',
                'areaAtuacaoMedico' => '',
            ],
            'page' => 1,
            'pageNumber' => 1,
            'pageSize' => 10,
        ]
    ];

    try {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // Disable SSL verification (for testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }
        
        return $result;
    } catch (Exception $e) {
        echo 'Error in fetchDoctors: ' . $e->getMessage() . PHP_EOL;
        return null;
    }
}

/**
 * Fetches a doctor's photo using security hash, CRM, and UF.
 *
 * @param string $securityHash The security hash for the request.
 * @param string $crm The CRM number of the doctor.
 * @param string $uf The UF code of the doctor's state.
 * @return array|null The decoded JSON response or null on error.
 */
function fetchDoctorPhoto(string $securityHash, string $crm, string $uf): ?array
{
    $url = 'https://portal.cfm.org.br/api_rest_php/api/v1/medicos/buscar_foto';
    
    $data = [
        [
            'securityHash' => $securityHash,
            'crm' => $crm,
            'uf' => $uf,
        ]
    ];

    try {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // Disable SSL verification (for testing only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }
        
        return $result;
    } catch (Exception $e) {
        echo 'Error in fetchDoctorPhoto: ' . $e->getMessage() . PHP_EOL;
        return null;
    }
}

/**
 * Main function to execute the doctor data and photo fetching process.
 */
function main(): void
{
    echo 'Fetch via PHP!' . PHP_EOL;
    
    $doctorData = fetchDoctors();
    
    if ($doctorData === null || !isset($doctorData['dados']) || !is_array($doctorData['dados'])) {
        echo 'No valid doctor data received.' . PHP_EOL;
        return;
    }
    
    $dados = $doctorData['dados'];
    
    foreach ($dados as $item) {
        if (isset($item['SECURITYHASH'], $item['NU_CRM'], $item['SG_UF'])) {
            $photoData = fetchDoctorPhoto($item['SECURITYHASH'], $item['NU_CRM'], $item['SG_UF']);
            echo"<pre>";
            echo 'Doctor Data: ' . print_r($item, true) . PHP_EOL;
            echo 'Photo Data: ' . print_r($photoData, true) . PHP_EOL;
            echo"</pre>";
        } else {
            echo 'Missing required fields in doctor data: ' . print_r($item, true) . PHP_EOL;
        }
    }
}

// header("Location: Valida_cadastro.php");

// Execute the main function
main();

?>