<?php
namespace DJEP\Resources;

class MusicRequests extends Resource
{
    /**
     * Get music requests for a specific event
     */
    public function forEvent(int $eventid): object
    {
        return $this->http->get('event_music_requests', ['eventid' => $eventid]);
    }

    /**
     * Add a music request to an event
     *
     * @param int    $eventid   The event
     * @param string $artist    Artist name
     * @param string $song      Song title
     * @param string $type      Request type: MPL, PIP, DED, DNP, OIR, GRQ, CR1-CR5
     * @param string $comments  Optional dedication or notes
     */
    public function add(int $eventid, string $artist, string $song, string $type, string $comments = ''): object
    {
        $data = [
            'eventid' => $eventid,
            'artist' => $artist,
            'song' => $song,
            'type' => $type,
        ];
        if (strlen($comments) > 0) {
            $data['comments'] = $comments;
        }
        return $this->http->post('add_music_request', $data);
    }
}
