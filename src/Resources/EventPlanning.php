<?php
namespace DJEP\Resources;

class EventPlanning extends Resource
{
    /**
     * Get planning form data for a specific event
     *
     * Returns the primary and additional planning forms with their question/answer data.
     * Note: this endpoint previously returned to-do items. Those now live on
     * event_tasks - use $djep->events->tasks($eventid).
     */
    public function forEvent(int $eventid): object
    {
        return $this->http->get('event_planning', ['eventid' => $eventid]);
    }
}
