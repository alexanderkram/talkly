<?php

namespace TobiasOlry\Talkly\Service;

use TobiasOlry\Talkly\Entity\User;
use TobiasOlry\Talkly\Entity\Topic;

use TobiasOlry\Talkly\Event\Events;
use TobiasOlry\Talkly\Event\TopicEvent;
use TobiasOlry\Talkly\Event\CommentEvent;

use Doctrine\ORM\EntityManager;

/**
 *
 * @author Daniel Badura <d.m.badura@gmail.com>
 */
class TopicService
{

    private $em;
    private $topicRepository;
    private $eventDispatcher;

    public function __construct(EntityManager $em, $eventDispatcher)
    {
        $this->em              = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->topicRepository = $em->getRepository('TobiasOlry\Talkly\Entity\Topic');
    }

    public function getTopic($id, $allowArchived = false)
    {
        if (empty($id)) {
            // todo
        }

        $topic = $this->topicRepository->find($id);

        if (! $topic) {
            // todo
        }

        if (! $allowArchived && $topic->isLectureHeld()) {
            // todo
        }

        return $topic;
    }

    /**
     *
     * @param Topic $topic
     * @param User $user
     */
    public function addVote(Topic $topic, User $user)
    {
        if ($topic->getVotes()->contains($user)) {

            return;
        }

        $topic->getVotes()->add($user);
        $user->getVotes()->add($topic);

        $this->em->flush();
    }

    /**
     *
     * @param Topic Topic
     * @param User $user
     */
    public function removeVote(Topic $topic, User $user)
    {
        if (!$topic->getVotes()->contains($user)) {

            return;
        }

        $topic->getVotes()->removeElement($user);
        $user->getVotes()->removeElement($topic);

        $this->em->flush();
    }

    public function addSpeaker(Topic $topic, User $user)
    {
        if (!$topic->getSpeakers()->contains($user)) {
            $topic->getSpeakers()->add($user);
            $user->getSpeakingTopics()->add($topic);
        }

        $this->em->flush();
    }

    public function removeSpeaker(Topic $topic, User $user)
    {
        if ($topic->getSpeakers()->contains($user)) {
            $topic->getSpeakers()->removeElement($user);
            $user->getSpeakingTopics()->removeElement($topic);
        }

        $this->em->flush();
    }

    public function comment(Topic $topic, User $user, $comment)
    {
        $comment = $topic->comment($user, $comment);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Events::COMMENT_CREATED,
            new CommentEvent($comment)
        );
    }

    public function findNonArchivedMostVotesFirst()
    {
        return $this->topicRepository->findNonArchivedMostVotesFirst();
    }

    public function findLastSubmissions($limit = 8)
    {
        return $this->topicRepository->findLastSubmissions($limit);
    }

    public function findArchivedGroupByMonth()
    {
        return $this->topicRepository->findArchivedGroupByMonth();
    }

    public function findNextTopics($limit = 5)
    {
        return $this->topicRepository->findNextTopics($limit);
    }

    public function findNextGroupByMonth()
    {
        return $this->topicRepository->findNextGroupByMonth();
    }

    public function add(Topic $topic)
    {
        $this->em->persist($topic);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            Events::TOPIC_CREATED,
            new TopicEvent($topic)
        );
    }

    public function update(Topic $topic)
    {
        $this->em->flush();
    }
}
