<?php

namespace App\Domain\StateMachine;

enum SignalState
{
    case PENIDING;
    case ACTIVE;
    case HIT_TP;
    case HIT_SL;
    case CANCELED;
    case EXPIRED;
}
