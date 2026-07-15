<?php

namespace App\Infra\Audit;

use App\Domain\Contract\SignalDispatchableInterface;
use App\Domain\Contract\SignalListenerInterface;
use App\Domain\Event\SignalTransitionFailed;
use App\Domain\Event\SignalTransitionSucceeded;

class StateTransitionListener implements SignalListenerInterface
{
    public function handle(SignalDispatchableInterface $event)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'signal_state_transition_audit';
        if ($event instanceof SignalTransitionFailed) {
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO {$table} (signal_id, from_state, to_state, is_succeeded, failure_msg) 
                   VALUES (%d, %s, %s, %d, %s)",
                    $this->event->getId(),
                    $event->data['from_state']->name,
                    $event->data['to_state']->name,
                    0,
                    $event->data['failure_msg'],
                ),
            );
        }

        if ($event instanceof SignalTransitionSucceeded) {
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO {$table} (signal_id, from_state, to_state, is_succeeded, failure_msg) 
                   VALUES (%d, %s, %s, %d, %s)",
                    $event->signal->getId(),
                    $event->data['from_state']->name,
                    $event->data['to_state']->name,
                    1,
                    $event->data['failure_msg'],
                ),
            );

        }

    }
}
