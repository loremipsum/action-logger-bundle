<?php

namespace LoremIpsum\ActionLoggerBundle\Action;

use DateTime;

trait ChangeSet
{
    protected function getChanges(array $changeSet, array $skip = [])
    {
        $skip = array_merge($skip, ['createdAt', 'createdBy', 'updatedAt', 'updatedBy']);
        foreach ($skip as $key) {
            unset($changeSet[$key]);
        }
        if (empty($changeSet)) {
            return ['no loggable changes', []];
        }

        $msg    = [];
        $params = [];
        foreach ($changeSet as $key => $values) {
            $msg[] = "<strong>%cskey__{$key}%</strong> from '%csval__{$key}__0%' to '%csval__{$key}__1%'";

            $params['%cskey__' . $key . '%']    = $key;
            $params['%csval__' . $key . '__0%'] = $this->getChangeSetValue($values[0]);
            $params['%csval__' . $key . '__1%'] = $this->getChangeSetValue($values[1]);
        }
        return ['<ul><li>' . implode('</li><li>', $msg) . '</li></ul>', $params];
    }

    private function getChangeSetValue($value)
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_scalar($value)) {
            return (string)$value;
        }
        if (! is_array($value)
            && ((! is_object($value) && settype($value, 'string') !== false)
                || (is_object($value) && method_exists($value, '__toString')))
        ) {
            return (string)$value;
        }
        return json_encode($value);
    }

    /**
     * @param array         $old
     * @param array         $new
     * @param array         $skip
     * @param callable|null $equals function (string $key, mixed $oldValue, mixed $newValue) { return $oldValue === $newValue; }
     * @return array diff between old and new as list of key => [old-value, new-value]
     */
    protected function getChangeSet(array $old, array $new, array $skip = [], callable $equals = null)
    {
        $diff = [];
        foreach (array_keys($old + $new) as $key) {
            if (in_array($key, $skip)) {
                continue;
            }
            $oldValue = array_key_exists($key, $old) ? $old[$key] : null;
            $newValue = array_key_exists($key, $new) ? $new[$key] : null;
            if (($equals && ! $equals($key, $oldValue, $newValue)) || (! $equals && $oldValue !== $newValue)) {
                $diff[$key] = [$oldValue, $newValue];
            }
        }
        return $diff;
    }
}
