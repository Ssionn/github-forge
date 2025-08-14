<?php

namespace Ssionn\GithubForgeLaravel\Enums;

enum TokenType: string
{
    case GHP = 'ghp_';
    case PAT = 'github_pat_';

}
