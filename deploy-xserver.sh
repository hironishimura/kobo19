#!/bin/bash
#
# テーマを Xserver に反映する。
#
#   ./deploy-xserver.sh          反映する
#   ./deploy-xserver.sh --check  何が変わるかだけ見る（サーバーは触らない）
#
# 事前に .deploy.env を用意し、Xserver 側で SSH を ON にしておきます
# （docs/xserver-setup.md の手順5-B）。
#
set -euo pipefail
cd "$(dirname "$0")"

if [ ! -f .deploy.env ]; then
	echo ".deploy.env がありません。.deploy.env.example を写して、値を埋めてください。" >&2
	exit 1
fi

# shellcheck disable=SC1091
source .deploy.env

: "${XSERVER_ID:?XSERVER_ID が空です}"
: "${XSERVER_HOST:?XSERVER_HOST が空です}"
: "${XSERVER_KEY:?XSERVER_KEY が空です}"
: "${XSERVER_DOMAIN:?XSERVER_DOMAIN が空です}"

KEY="${XSERVER_KEY/#\~/$HOME}"
SRC="wordpress/wp-content/themes/kobo19/"
DEST="/home/${XSERVER_ID}/${XSERVER_DOMAIN}/public_html/wp-content/themes/kobo19/"

if [ ! -f "$KEY" ]; then
	echo "秘密鍵が見つかりません: $KEY" >&2
	exit 1
fi

DRY=""
if [ "${1:-}" = "--check" ]; then
	DRY="--dry-run"
	echo "（確認だけ。サーバーには書き込みません）"
fi

echo "反映先: ${XSERVER_ID}@${XSERVER_HOST}:${DEST}"

# 送る前に PHP の構文だけ確かめる
for f in $(find "$SRC" -name "*.php"); do
	php -l "$f" >/dev/null || { echo "構文エラー: $f" >&2; exit 1; }
done

rsync -avz --delete $DRY \
	--exclude ".DS_Store" \
	-e "ssh -p 10022 -i ${KEY} -o StrictHostKeyChecking=accept-new" \
	"$SRC" \
	"${XSERVER_ID}@${XSERVER_HOST}:${DEST}"

if [ -z "$DRY" ]; then
	echo
	echo "反映しました。https://${XSERVER_DOMAIN}/ を開いて確かめてください。"
	echo "初回は 管理画面 → 外観 → テーマ で「19工房」を有効化してください。"
fi
