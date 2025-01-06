<?php

namespace App\Repositories;

class BaseRepository
{
    /*
     * @var \Illuminate\Database\Eloquent\Model;
     * */
    protected $model;

    public function all()
    {
        return $this->model->get();
    }

    public function show($uuid)
    {
        return $this->model->findByUUIDOrFail($uuid);
    }

    public function destroy($uuid)
    {
        return $this->model->findByUUIDOrFail($uuid)->delete();
    }

    public function destroyMultiple($uuid)
    {
        return $this->model->WhereInUUID($uuid)->delete();
    }
}
