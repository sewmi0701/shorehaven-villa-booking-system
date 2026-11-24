# Save approved example-room stock photography for offline XAMPP display.
# This changes files only; it never touches the MySQL database.
$ErrorActionPreference = 'Stop'
$photoDir = Join-Path $PSScriptRoot 'assets\images\photos'
New-Item -ItemType Directory -Path $photoDir -Force | Out-Null
$urls = [ordered]@{
    'ocean' = 'https://images.unsplash.com/photo-1718942899965-4fc10607d805?auto=format&fit=crop&w=1400&q=82'
    'palm' = 'https://images.unsplash.com/photo-1762117360890-5eacdbb07b04?auto=format&fit=crop&w=1400&q=82'
    'garden' = 'https://images.unsplash.com/photo-1763914767111-b4451913ceba?auto=format&fit=crop&w=1400&q=82'
    'sunset' = 'https://images.unsplash.com/photo-1721369483526-62f48a00b949?auto=format&fit=crop&w=1400&q=82'
    'standard' = 'https://images.unsplash.com/photo-1777543391649-9c635a4dfcf6?auto=format&fit=crop&w=1400&q=82'
    'deluxe' = 'https://images.unsplash.com/photo-1690314754158-7287f2022d9d?auto=format&fit=crop&w=1400&q=82'
}
$failed = @()
foreach ($item in $urls.GetEnumerator()) {
    $filename = if ($item.Key -in @('palm','garden','standard','deluxe')) { $item.Key + '-approved.jpg' } else { $item.Key + '.jpg' }
    $destination = Join-Path $photoDir $filename
    try {
        Write-Host ('Downloading ' + $item.Key + '...') -ForegroundColor Cyan
        Invoke-WebRequest -Uri $item.Value -OutFile $destination -UseBasicParsing -TimeoutSec 45
        if ((Get-Item $destination).Length -lt 10000) { throw 'File is too small' }
        Write-Host ('Saved: ' + $destination) -ForegroundColor Green
    } catch {
        if (Test-Path $destination) { Remove-Item $destination -Force }
        Write-Host ('Could not download ' + $item.Key + ': ' + $_.Exception.Message) -ForegroundColor Yellow
        $failed += $item.Key
    }
}
if ($failed.Count -gt 0) { Write-Host ('Photos not downloaded: ' + ($failed -join ', ')); exit 1 }
Write-Host 'All six photos saved. Refresh your website with Ctrl+F5.' -ForegroundColor Green
