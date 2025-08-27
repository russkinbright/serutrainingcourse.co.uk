# Auto-commit & push tracked changes every 10s
# Runs locally on Windows. Does NOT touch .env or vendor/ if they're ignored.

# If this script sits in <project>\scripts, the project root is one level up:
$folder  = (Resolve-Path "$PSScriptRoot\..").Path   # change to $PSScriptRoot if you put the script in the project root
$branch  = "main"
$seconds = 1
$log     = Join-Path $folder "auto-commit.log"
$kill    = Join-Path $folder ".auto-commit.stop"    # create this file to stop the loop

# make sure Git uses the credential manager (only needs to be set once on your machine)
git config --global credential.helper manager 2>$null | Out-Null

Write-Host "Auto-commit watching: $folder (branch: $branch, every $seconds s)"
"[$(Get-Date -Format s)] start watching $folder" | Out-File -Append $log

while ($true) {
  if (Test-Path $kill) {
    "[$(Get-Date -Format s)] stop flag detected, exiting" | Out-File -Append $log
    break
  }

  # only tracked-file changes (ignores untracked like .env/vendor)
  $status = git -C $folder status --porcelain --untracked-files=no

  if ($LASTEXITCODE -ne 0) {
    "[$(Get-Date -Format s)] git status failed" | Out-File -Append $log
    Start-Sleep -Seconds $seconds
    continue
  }

  if ($status) {
    try {
      # fetch latest and rebase (auto-stash local edits)
      git -C $folder pull --rebase --autostash origin $branch | Out-Null

      # commit only tracked file changes
      git -C $folder commit -am ("Auto-commit: {0:yyyy-MM-dd HH:mm:ss}" -f (Get-Date)) | Out-Null

      # push
      git -C $folder push origin $branch | Out-Null

      $msg = "[$(Get-Date -Format s)] pushed"
      Write-Host $msg
      $msg | Out-File -Append $log
    } catch {
      $err = "[$(Get-Date -Format s)] ERROR: $($_.Exception.Message)"
      Write-Warning $err
      $err | Out-File -Append $log
    }
  }

  Start-Sleep -Seconds $seconds
}
