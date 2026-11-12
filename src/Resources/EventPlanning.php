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

    /**
     * Write answers back to a planning form.
     *
     * @param int   $eventid  The event
     * @param array $answers  question_id => answer. For grids, use the cell name
     *                        as the key: GUESTTABLE_ROW_2_COLUMN_1 => 'Jane'.
     * @param int   $formid   Only for additional forms. Omit for the primary.
     *
     * Only the questions you pass are touched. Everything is validated before
     * anything is written, so one bad value means nothing changes.
     *
     * Read the form first with forEvent() to get the question ids, types and
     * options. Question ids are stable, so they can be cached.
     *
     *     $djep->planning->updateAnswers(252318, [
     *         'MEALTYPE'   => 'Buffet',
     *         'GUESTCOUNT' => '180',
     *     ]);
     */
    public function updateAnswers(int $eventid, array $answers, int $formid = 0): object
    {
        $data = $answers;
        $data['eventid'] = $eventid;

        // Grid cells are sent as ID_ROW_n_COLUMN_n but 'fields' takes the
        // question id, so collapse those back to the question they belong to.
        $questionIds = [];
        foreach (array_keys($answers) as $key) {
            $questionIds[preg_replace('/_ROW_\d+_COLUMN_\d+$/', '', $key)] = true;
        }
        $data['fields'] = implode(',', array_keys($questionIds));

        if ($formid > 0) {
            $data['formid'] = $formid;
        }
        return $this->http->post('update_event_planning', $data);
    }

    /**
     * Replace one of an event's two timelines.
     *
     * @param int    $eventid
     * @param array  $activities  Rows in running order, each with any of
     *                            'time', 'name' and 'comments'. Up to 52 are
     *                            stored; anything past that is dropped. Pass an
     *                            empty array to clear the timeline.
     * @param string $headline    Text shown above the activities.
     * @param int    $slot        1 for the timeline, 2 for the second timeline.
     *
     * This REPLACES the whole timeline, so send every activity you want kept.
     * Clearing it removes the headline too, because a timeline with a headline
     * and no activities reads back as one blank activity.
     *
     *     $djep->planning->updateTimeline(252318, [
     *         ['time' => '6:00 PM', 'name' => 'Guests arrive'],
     *         ['time' => '7:30 PM', 'name' => 'First dance', 'comments' => 'Dim the lights'],
     *     ], 'Reception running order');
     */
    public function updateTimeline(int $eventid, array $activities, string $headline = '', int $slot = 1): object
    {
        $data = [
            'eventid'  => $eventid,
            'slot'     => $slot,
            'headline' => $headline,
        ];
        $i = 1;
        foreach ($activities as $activity) {
            if ($i > 52) {
                break;
            }
            $data["activity_{$i}_time"]     = $activity['time'] ?? '';
            $data["activity_{$i}_name"]     = $activity['name'] ?? '';
            $data["activity_{$i}_comments"] = $activity['comments'] ?? '';
            $i++;
        }
        return $this->http->post('update_timeline', $data);
    }
}
