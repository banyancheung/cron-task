<?php declare(strict_types=1);

namespace Swoft\CronTask\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Scheduled annotation
 *
 * @Annotation
 * @Target("METHOD")
 * @Attributes(
 *     @Attribute("cron", type="string"),
 *     @Attribute("description", type="string")
 * )
 */
class Scheduled
{

    /**
     * @var string
     * @Required()
     */
    private $cron;

    /**
     * @var string
     */
    private $description = '';

    /**
     * Bean constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->cron = $values['value'];
        }

        if (isset($values['cron'])) {
            $this->cron = $values['cron'];
        }

        if (isset($values['description'])) {
            $this->description = $values['description'];
        }
    }

    /**
     * @return string
     */
    public function getCron(): string
    {
        return $this->cron;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

}
