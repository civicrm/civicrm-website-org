#!/bin/bash
set -e
REMOTE="$1"

if [ -z "$REMOTE" ]; then
  echo "Description: Create and push a date-based tag"
  echo "Usage: $0 <push-to-remote>"
  echo "  <push-to-remote>  The name of the git remote to which we should immediately push the tag."
  echo "Example: $0 origin"
  exit
fi

set -ex
TAG=deploy-$(date -u '+%Y-%m-%d-%H-%M')
git tag -a "$TAG" -m "Deployment tag ($TAG)"
git push "$REMOTE" "$TAG"
