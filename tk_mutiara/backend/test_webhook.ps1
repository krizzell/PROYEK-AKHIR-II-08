# Calculate SHA512 signature
$orderID = "TAGIHAN-390-1779637800"
$statusCode = "200"
$grossAmount = "250000"
$serverKey = "Mid-server-MJWxOJRM3Sc_PLcUClYtFFAC"
$raw = $orderID + $statusCode + $grossAmount + $serverKey

# Compute SHA512
$hasher = [System.Security.Cryptography.SHA512]::Create()
$hash = $hasher.ComputeHash([System.Text.Encoding]::UTF8.GetBytes($raw))
$signature = [BitConverter]::ToString($hash) -replace '-', ''

Write-Host "Signature: $signature" -ForegroundColor Green

# Send webhook
$payload = @{
    order_id = $orderID
    transaction_status = "settlement"
    transaction_id = "0200001234567890"
    payment_type = "credit_card"
    gross_amount = $grossAmount
    currency = "IDR"
    merchant_id = "M123456"
    status_code = $statusCode
    status_message = "midtrans payment success"
    signature_key = $signature
    transaction_time = "2026-05-23 22:50:31"
    fraud_status = "accept"
} | ConvertTo-Json

Write-Host "Sending webhook..." -ForegroundColor Cyan

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8081/api/payment/webhook/midtrans" -Method Post -Body $payload -ContentType "application/json" -UseBasicParsing -ErrorAction Stop
    Write-Host "SUCCESS! Status: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "Response: $($response.Content)" -ForegroundColor Green
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $errorBody = $reader.ReadToEnd()
        Write-Host "Error Body: $errorBody" -ForegroundColor Red
    }
}
