<?php
namespace App\Service\AuditLog;

final class ChangeSetComparator
{
    public function diff(
        array $before,
        array $after
    ): array {
        $changes = [];
        
        foreach($before as $field => $oldValue){
            
            if(!array_key_exists($field, $after)) continue;

            $newValue = $after[$field];

            if($this->valuesDiffer($oldValue, $newValue)) {
                $changes[$field] = [
                    'old' => $this->normalize($oldValue),
                    'new' => $this->normalize($newValue)
                ];
            }

        }

        return $changes;
    }

    private function valuesDiffer(
        mixed $a, 
        mixed $b
    ) : bool {
        return $a !== $b;
    }

    private function normalize(
        mixed $value
    ): mixed {
        if($value instanceof \DateTimeInterface) return $value->format(DATE_ATOM);

        if(is_object($value) && method_exists($value, 'getId')) return $value->getId();

        return $value;
    }
}