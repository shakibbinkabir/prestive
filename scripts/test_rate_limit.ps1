$ErrorActionPreference = 'Stop'

$base = "http://127.0.0.1:8000"

Write-Host "Fetching CSRF token and establishing session..."
$resp = Invoke-WebRequest -Uri "$base/" -SessionVariable sess
$html = $resp.Content
$m = [regex]::Match($html, '<meta\s+name=\"csrf-token\"\s+content=\"([^\"]+)\"')
if (-not $m.Success) {
  throw "Failed to extract CSRF token from homepage"
}
$csrf = $m.Groups[1].Value
Write-Host "CSRF token acquired: $($csrf.Substring(0,8))..."

$results = @()
for ($i=1; $i -le 65; $i++) {
  # Send empty data to avoid DB writes; we only want to observe 429 after 60 requests/min
  $body = @{ data = @{} } | ConvertTo-Json -Depth 5
  try {
    $r = Invoke-WebRequest -Uri "$base/api/membership/draft/save" -Method POST -WebSession $sess -Headers @{ 'Content-Type'='application/json'; 'X-CSRF-Token'=$csrf } -Body $body
    $status = [int]$r.StatusCode
  } catch {
    # For non-2xx responses Invoke-WebRequest throws; capture the HTTP status code
    $status = [int]$_.Exception.Response.StatusCode.value__
  }
  $results += [pscustomobject]@{ i=$i; status=$status }
}

"\nSummary (by status):"
$results | Group-Object status | Sort-Object Name | ForEach-Object { "Status $($_.Name): $($_.Count)" }

"\nFirst 10 results:"
$results | Select-Object -First 10 | Format-Table -AutoSize

"\nLast 10 results:"
$results | Select-Object -Last 10 | Format-Table -AutoSize
