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
}
