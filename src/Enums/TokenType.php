<?php

namespace Ssionn\GithubForgeLaravel\Enums;

enum TokenType: string
{
    case GHP = 'ghp_';
    case GHT = 'gho_';
    case GHS = 'ghs_';

    case PAT = 'github_pat_';

}
