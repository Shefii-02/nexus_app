#!/bin/bash
cd /var/www/univexa.in/nexus_app/admin_frontend
git pull
npm install
npm run build
echo "✅ Deploy done"