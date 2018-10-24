<?php

namespace LoremIpsum\ActionLoggerBundle;

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
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }

    /**
     * @param array $old
     * @param array $new
     * @param array $skip
     * @return array diff between old and new as list of key => [old-value, new-value]
     */
    protected function getChangeSet(array $old, array $new, array $skip = [])
    {
        $diff = [];
        foreach (array_keys($old + $new) as $key) {
            if (in_array($key, $skip)) {
                continue;
            }
            $oldValue = array_key_exists($key, $old) ? $old[$key] : null;
            $newValue = array_key_exists($key, $new) ? $new[$key] : null;
            if ($oldValue != $newValue) {
                $diff[$key] = [$oldValue, $newValue];
            }
        }
        return $diff;
    }
}
