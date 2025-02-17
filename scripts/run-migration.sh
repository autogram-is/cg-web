rm ./migration.log
wp cg hierarchy --allow-root 2>&1 | tee migration.log
wp cg projects --allow-root 2>&1 | tee migration.log
wp cg posts --allow-root 2>&1 | tee migration.log
wp cg events --allow-root 2>&1 | tee migration.log
wp cg pages --allow-root 2>&1 | tee migration.log
wp cg tags --allow-root 2>&1 | tee migration.log
wp cg navigation --allow-root 2>&1 | tee migration.log
wp cg import --allow-root 2>&1 | tee migration.log
