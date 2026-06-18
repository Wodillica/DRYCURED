# Drycured Git secret hygiene report

Status: **COMPLETED**

Date: 2026-06-18_18-57-48

## Context

GitHub push protection blocked a push because a SQL database backup contained a detected Anthropic API key pattern. The SQL backup was removed from the Git commit, copied outside the repository and the amended commit was pushed successfully.

## Current safe commit

- Current HEAD: `4e84541`
- Expected safe commit: `b6582e2`

## Actions performed

- SQL backup removed from the committed Git tree.
- SQL backup copy kept outside Git in: `/root/DRYCURED_SENSITIVE_BACKUPS/recipe_master/`
- SQL backup patterns added/confirmed in `.gitignore`.
- Sensitive backup policy README added/confirmed.
- Git reflog expired.
- Unreachable Git objects pruned.
- Reachable tracked files scanned for `sk-ant-` pattern.

## Important security note

The detected Anthropic API key must be treated as exposed and should be rotated/revoked in the provider/account where it was created.

## WordPress status

This hygiene step does not modify WordPress.
