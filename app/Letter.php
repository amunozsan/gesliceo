<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum Letter:string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case H = 'H';
    case I = 'I';
    case J = 'J';
    case K = 'K';
    case L = 'L';
    case M = 'M';
    case N = 'N';
    case O = 'O';
    case P = 'P';
    case Q = 'Q';
    case R = 'R';
    case S = 'S';
    case T = 'T';
    case U = 'U';
    case V = 'V';
    case W = 'W';
    case X = 'X';
    case Y = 'Y';
    case Z = 'Z';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
