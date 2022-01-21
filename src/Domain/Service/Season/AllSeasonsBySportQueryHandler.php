<?php

namespace Tailgate\Domain\Service\Season;

use Tailgate\Application\Query\Season\AllSeasonsBySportQuery;
use Tailgate\Domain\Model\Season\SeasonViewRepositoryInterface;
use Tailgate\Domain\Service\DataTransformer\SeasonDataTransformerInterface;

class AllSeasonsBySportQueryHandler
{
    private $seasonViewRepository;
    private $seasonViewTransformer;

    public function __construct(
        SeasonViewRepositoryInterface $seasonViewRepository,
        SeasonDataTransformerInterface $seasonViewTransformer
    ) {
        $this->seasonViewRepository = $seasonViewRepository;
        $this->seasonViewTransformer = $seasonViewTransformer;
    }

    public function handle(AllSeasonsBySportQuery $query)
    {
        $seasonViews = $this->seasonViewRepository->allBySport($query->getSport());

        $seasons = [];

        foreach ($seasonViews as $seasonView) {
            $seasons[] = $this->seasonViewTransformer->read($seasonView);
        }

        return $seasons;
    }
}
