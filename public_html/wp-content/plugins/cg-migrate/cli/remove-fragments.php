<?php

function cg_remove_fragments($dry_run = false) {
  WP_CLI::log(($dry_run ? "Dry Run: " : "") ."Deleting post fragments");
}