<?php

namespace AndreSuntoro\Database\Eloquent;

/**
 * Soft Expires
 * andresuntoro@gmail.com
 */
Trait SoftExpires
{
    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftExpires()
    {
    	$static = new static;
        static::addGlobalScope(new SoftExpiringScope);
    }

    /**
     * Initialize the soft deleting trait for an instance.
     *
     * @return void
     */
    public function initializeSoftExpires()
    {
        if (! isset($this->casts[$this->getExpiredAtColumn()])) {
            $this->casts[$this->getExpiredAtColumn()] = 'datetime';
        }
        if (! isset($this->attributes[$this->getExpiredAtColumn()])) {
            $time = $this->freshTimestamp()->addSeconds($this->getExpiredAtValue());
            $this->attributes[$this->getExpiredAtColumn()] = $this->fromDateTime($time);
        }
    }

    /**
     * Get the name of the "expired at" column.
     *
     * @return string
     */
    public function getExpiredAtColumn()
    {
        return defined('static::EXPIRED_AT') ? static::EXPIRED_AT : 'expired_at';
    }

    /**
     * Get the name of the "expired at" column.
     *
     * @return string
     */
    public function getExpiredAtValue()
    {
        return defined('static::EXPIRED_AT_VALUE') ? static::EXPIRED_AT_VALUE : 300;
    }

    /**
     * Get the fully qualified "expired at" column.
     *
     * @return string
     */
    public function getQualifiedExpiredAtColumn()
    {
        return $this->qualifyColumn($this->getExpiredAtColumn());
    }
}