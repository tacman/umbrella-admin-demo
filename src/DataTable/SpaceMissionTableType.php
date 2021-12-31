<?php

namespace App\DataTable;

use App\DataTable\Column\CostColumnType;
use App\DataTable\Column\LocationColumnType;
use App\DataTable\Column\StatusColumnType;
use App\Entity\SpaceMission;
use App\Form\Base\MissionStatusChoiceType;
use Doctrine\ORM\QueryBuilder;
use Umbrella\CoreBundle\DataTable\Adapter\EntityAdapter;
use Umbrella\CoreBundle\DataTable\Column\DateColumnType;
use Umbrella\CoreBundle\DataTable\DataTableBuilder;
use Umbrella\CoreBundle\DataTable\DataTableType;
use Umbrella\CoreBundle\Form\DatepickerType;
use Umbrella\CoreBundle\Form\SearchType;

class SpaceMissionTableType extends DataTableType
{
    public function buildTable(DataTableBuilder $builder, array $options)
    {
        $builder
            ->addFilter('search', SearchType::class)
            ->addFilter('from', DatepickerType::class, [
                'input_prefix_text' => 'From'
            ])
            ->addFilter('to', DatepickerType::class, [
                'input_prefix_text' => 'To'
            ])
            ->addFilter('missionStatus', MissionStatusChoiceType::class, [
                'required' => false,
                'placeholder' => 'Mission status'
            ]);

        $builder
            ->add('date', DateColumnType::class, [
                'order' => 'DESC',
                'format' => 'd M Y'
            ])
            ->add('companyName')
            ->add('location', LocationColumnType::class)
            ->add('detail')
            ->add('cost', CostColumnType::class)
            ->add('rocketStatus', StatusColumnType::class)
            ->add('missionStatus', StatusColumnType::class);

        $builder->useAdapter(EntityAdapter::class, [
            'class' => SpaceMission::class,
            'query' => function (QueryBuilder $qb, array $formData) {
                if (isset($formData['search'])) {
                    $qb->andWhere('LOWER(e.search) LIKE :search');
                    $qb->setParameter('search', '%' . $formData['search'] . '%');
                }

                if (isset($formData['missionStatus'])) {
                    $qb->andWhere('e.missionStatus = :missionStatus');
                    $qb->setParameter('missionStatus', $formData['missionStatus']);
                }

                if (isset($formData['from'])) {
                    $qb->andWhere('e.date >= :from');
                    $qb->setParameter('from', $formData['from']);
                }

                if (isset($formData['to'])) {
                    $qb->andWhere('e.date <= :to');
                    $qb->setParameter('to', $formData['to']);
                }
            }
        ]);
    }
}
