# Auto-commit & push tracked changes every N seconds
# Windows / PowerShell
# - Only commits tracked file changes (untracked .env/vendor remain ignored if in .gitignore)
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

  # only tracked-file changes (ignores untracked like .env/vendor)
  $status = git -C $folder status --porcelain --untracked-files=no

  if ($LASTEXITCODE -ne 0) {
    "[$(Get-Date -Format s)] git status failed" | Out-File -Append $log
    Start-Sleep -Seconds $seconds
    continue
  }

  if ($status) {
    # show a brief summary
    $changes = git -C $folder diff --name-status
    if (-not $changes) { $changes = "(changes detected but diff empty - maybe whitespace/line-endings)" }
    Write-Host "`n=== Changes detected ==="
    $changes | Write-Host
    Write-Host "========================`n"

    # prompt for commit message (required)
    do {
      $userMsg = Read-Host "Enter commit message"
      if ([string]::IsNullOrWhiteSpace($userMsg)) { Write-Warning "Commit message cannot be empty." }
    } while ([string]::IsNullOrWhiteSpace($userMsg))

    # append timestamp
    $commitMsg = "{0} ({1:yyyy-MM-dd HH:mm:ss})" -f $userMsg, (Get-Date)

    try {
      # fetch latest and rebase (auto-stash can be configured globally; CLI flag can be flaky on some setups)
      git -C $folder pull --rebase origin $branch | Out-Null

      # commit only tracked file changes with your message + timestamp
      git -C $folder commit -am $commitMsg | Out-Null
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
