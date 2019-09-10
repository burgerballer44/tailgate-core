<?php

namespace Tailgate\Application\Query\Season;

use Tailgate\Domain\Model\Season\SeasonId;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Model\Season\GameViewRepositoryInterface;
use Tailgate\Application\DataTransformer\SeasonDataTransformerInterface;

class SeasonQueryHandler
{
    private $seasonViewRepository;
    private $gameViewRepository;
    private $seasonViewTransformer;

    public function __construct(
        SeasonViewRepositoryInterface $seasonViewRepository,
        GameViewRepositoryInterface $gameViewRepository,
        SeasonDataTransformerInterface $seasonViewTransformer
    ) {
        $this->seasonViewRepository = $seasonViewRepository;
        $this->gameViewRepository = $gameViewRepository;
        $this->seasonViewTransformer = $seasonViewTransformer;
    }

    public function handle(SeasonQuery $query)
    {
        $seasonId = SeasonId::fromString($query->getSeasonId());

        $seasonView = $this->seasonViewRepository->get($seasonId);
        $gameViews = $this->gameViewRepository->getAllBySeason($seasonId);

        $seasonView->addGameViews($gameViews);

        return $this->seasonViewTransformer->read($seasonView);
    }
}
