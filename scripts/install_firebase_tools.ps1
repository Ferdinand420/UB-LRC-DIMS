# Installs Firebase CLI on Windows using npm
echo "Checking for npm..."
try {
  $npmVersion = npm -v 2>$null
} catch {
  Write-Host "npm was not found. Please install Node.js from https://nodejs.org/ and re-run this script." -ForegroundColor Yellow
  exit 1
}

Write-Host "Installing firebase-tools globally..."
npm install -g firebase-tools | Out-Host

Write-Host "Verifying installation..."
try {
  $fbVersion = firebase --version
  Write-Host "Firebase CLI installed: $fbVersion" -ForegroundColor Green
  Write-Host "Log in: firebase login" -ForegroundColor Green
} catch {
  Write-Host "Failed to run 'firebase'. Ensure your global npm bin is on PATH." -ForegroundColor Red
  Write-Host "You may need to add %AppData%\npm to PATH or reopen your terminal." -ForegroundColor Yellow
  exit 1
}
