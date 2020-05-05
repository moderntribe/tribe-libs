#!/usr/bin/env bash
# Creates a new repository for a package

function help {
  echo "Creates a new repository for a package on GitHub"
  echo ""
  echo "Syntax:   $0 square1-repo1,square1-repo2"
}

shift $((OPTIND-1))

REPOS=$1

if [[ -z "$REPOS" ]]; then
   help
   exit 0
fi

ORG='moderntribe'
COL_USER='tr1b0t'

echo Enter your github user
read GH_USER

echo Enter Auth Token
read -s PASSWORD

array=(${REPOS//,/ })

for repo in "${array[@]}"; do
  echo "[INFO] Creating repository $ORG/$repo"
  curl -i -u "$GH_USER:$PASSWORD" -X POST --data "{\"name\":\"$repo\"}" "https://api.github.com/orgs/$ORG/repos" 2>&1 | grep message || echo "done"

  echo "[INFO] Adding $COL_USER to $ORG/$repo"
  curl -i -u "$GH_USER:$PASSWORD" -X PUT -d '' "https://api.github.com/repos/$ORG/$repo/collaborators/$COL_USER" 2>&1 | grep message || echo "done"
done

exit 0
