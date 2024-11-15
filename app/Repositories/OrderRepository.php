<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * get all order.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Order::all();
    }

    /**
     * find order by id.
     *
     * @param int $id
     * @return Order
     * @throws ModelNotFoundException
     */
    public function findById(int $id)
    {
        return Order::findOrFail($id);
    }

    /**
     * create a new order.
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data)
    {
        return Order::create($data);
    }

    /**
     * updated exists order.
     *
     * @param int $id
     * @param array $data
     * @return Order
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data)
    {
        $order = $this->findById($id);
        $order->update($data);
        return $order;
    }

    /**
     * delete order
     *
     * @param int $id
     * @return bool|null
     * @throws ModelNotFoundException
     */
    public function delete(int $id)
    {
        $order = $this->findById($id);
        return $order->delete();
    }
}
