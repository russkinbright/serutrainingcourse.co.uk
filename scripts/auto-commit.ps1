# Auto-commit & push tracked changes every N seconds
# Windows / PowerShell
# - Includes new files (respects .gitignore)
# - Always asks for a commit message and appends date/time
# - Keeps your original pull/commit/push flow

$ErrorActionPreference = 'Stop'  # make try/catch behave as expected

# If this script sits in <project>\scripts, the project root is one level up:
$folder  = (Resolve-Path "$PSScriptRoot\..").Path   # change to $PSScriptRoot if the script is in the repo root
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

  # detect any changes (tracked + untracked; respects .gitignore)
  $status = git -C $folder status --porcelain

  if ($LASTEXITCODE -ne 0) {
    "[$(Get-Date -Format s)] git status failed" | Out-File -Append $log
    Start-Sleep -Seconds $seconds
    continue
  }

  if ($status) {
    # show a brief summary (shows added/modified/untracked)
    Write-Host "`n=== Changes detected ==="
    $status | Write-Host
    Write-Host "========================`n"

    # prompt for commit message (required)
    do {
      $userMsg = Read-Host "Enter commit message"
      if ([string]::IsNullOrWhiteSpace($userMsg)) { Write-Warning "Commit message cannot be empty." }
    } while ([string]::IsNullOrWhiteSpace($userMsg))

    # append timestamp
    $commitMsg = "{0} ({1:yyyy-MM-dd HH:mm:ss})" -f $userMsg, (Get-Date)

    try {
      # fetch latest and rebase (if this causes issues, switch to: git -C $folder pull origin $branch)
      git -C $folder pull --rebase origin $branch | Out-Null

      # include new files too (respects .gitignore)
      git -C $folder add -A | Out-Null
      if ($LASTEXITCODE -ne 0) {
        $err = "add -A failed (code $LASTEXITCODE)"
        Write-Warning $err
        "[$(Get-Date -Format s)] $err" | Out-File -Append $log
        Start-Sleep -Seconds $seconds
        continue
      }

      # if nothing staged (can happen if only ignored files changed), skip
      $pending = git -C $folder diff --cached --name-only
      if (-not $pending) {
        "[$(Get-Date -Format s)] nothing to commit (only ignored or no effective changes)" | Out-File -Append $log
        Start-Sleep -Seconds $seconds
        continue
      }

      # commit all staged changes with your message + timestamp
      git -C $folder commit -m $commitMsg | Out-Null
      if ($LASTEXITCODE -ne 0) {
        $err = "commit failed (code $LASTEXITCODE) - not pushing"
        Write-Warning $err
        "[$(Get-Date -Format s)] $err" | Out-File -Append $log
        Start-Sleep -Seconds $seconds
        continue
      }

      # push (set upstream if missing)
      $hasUpstream = $false
      git -C $folder rev-parse --abbrev-ref --symbolic-full-name "@{u}" 1>$null 2>$null
      if ($LASTEXITCODE -eq 0) { $hasUpstream = $true }

      if ($hasUpstream) {
        git -C $folder push origin $branch | Out-Null
      } else {
        git -C $folder push -u origin $branch | Out-Null
      }

      if ($LASTEXITCODE -ne 0) {
        $err = "push failed (code $LASTEXITCODE)"
        Write-Warning $err
        "[$(Get-Date -Format s)] $err" | Out-File -Append $log
      } else {
        $msg = "[$(Get-Date -Format s)] pushed ($commitMsg)"
        Write-Host $msg
        $msg | Out-File -Append $log
      }
    } catch {
      $err = "[$(Get-Date -Format s)] ERROR: $($_.Exception.Message)"
      Write-Warning $err
      $err | Out-File -Append $log
    }
  }

  Start-Sleep -Seconds $seconds
}
