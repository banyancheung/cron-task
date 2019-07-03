<?php declare(strict_types=1);

namespace Swoft\CronTask\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\AnnotationException;
use Swoft\CronTask\Annotation\Mapping\Scheduled;
use Swoft\CronTask\TaskRegister;

/**
 * Scheduled annotation parser
 * @AnnotationParser(Scheduled::class)
 */
class ScheduledParser extends Parser
{

    /**
     * Parse object
     *
     * @param int $type Class or Method or Property
     * @param Scheduled $annotation Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_METHOD) {
            throw new AnnotationException('`@Scheduled` must be defined on class method!');
        }

        TaskRegister::bindTask(
            $this->className, $this->methodName, $annotation->getCron(), $annotation->getDescription()
        );

        return [];
    }

}
