New-Item -ItemType Directory -Force .git/hooks | Out-Null
Copy-Item tools/hooks/pre-push .git/hooks/pre-push -Force
Write-Host "Git hook pre-push berhasil dipasang."
