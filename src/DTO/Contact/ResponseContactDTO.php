<?php
final class ResponseContactDTO
{
    
    public function __construct(
        public int $id,
        public ?string $full_name,
        public string $email,
        public string $phone,
        public string $status,
        public string $type,
        public string $created_at,
    )
    {}
}