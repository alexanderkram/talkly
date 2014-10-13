<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace TobiasOlry\Talkly\Event;

class NotificationMessage
{
    public $subject;
    public $details;

    /**
     * @param string $subject markdown string
     * @param string $details markdown string
     * @static
     * @access public
     * @return NotificationMessage
     */
    public static function create($subject, $details)
    {
        $message = new self();
        $message->subject = $subject;
        $message->details = $details;

        return $message;
    }

}

