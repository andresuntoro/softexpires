<?php

namespace AndreSuntoro\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Soft Expiring Scope
 * andresuntoro@gmail.com
 */
class SoftExpiringScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected $extensions = ['Reset', 'WithExpired', 'WithoutExpired', 'OnlyExpired'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getQualifiedExpiredAtColumn(), '>=', $builder->getModel()->freshTimestampString())->orWhereNull($model->getQualifiedExpiredAtColumn());
    }

    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Get the "expired at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return string
     */
    protected function getExpiredAtColumn(Builder $builder)
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedExpiredAtColumn();
        }

        return $builder->getModel()->getExpiredAtColumn();
    }

    /**
     * Add the reset extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addReset(Builder $builder)
    {
        $builder->macro('reset', function (Builder $builder, $value = null) {
            $builder->withExpired();

            return $builder->update([$builder->getModel()->getExpiredAtColumn() => $value]);
        });
    }

    /**
     * Add the with-expired extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithExpired(Builder $builder)
    {
        $builder->macro('withExpired', function (Builder $builder, $withExpired = true) {
            if (! $withExpired) {
                return $builder->withoutExpired();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-expired extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutExpired(Builder $builder)
    {
        $builder->macro('withoutExpired', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)
            ->where($model->getQualifiedExpiredAtColumn(), '>=', $builder->getModel()->freshTimestampString())
            ->orWhereNull($model->getQualifiedExpiredAtColumn());

            return $builder;
        });
    }

    /**
     * Add the only-expired extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addOnlyExpired(Builder $builder)
    {
        $builder->macro('onlyExpired', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNotNull(
                $model->getQualifiedExpiredAtColumn()
            )
            ->whereNotNull($model->getQualifiedExpiredAtColumn())
            ->where($model->getQualifiedExpiredAtColumn(), '<=', $builder->getModel()->freshTimestampString())
            ;

            return $builder;
        });
    }
}