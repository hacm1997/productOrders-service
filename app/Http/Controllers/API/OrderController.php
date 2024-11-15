<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\OrderRepositoryInterface;
use App\Exports\OrdersExport;
use App\Services\ExcelExportService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="API Endpoints para órdenes"
 * )
 */

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Listar todas las órdenes",
     *     description="Devuelve una lista de órdenes",
     *     operationId="getOrders",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de órdenes"
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */

    public function index()
    {
        try {
            $orders = $this->orderRepository->getAll();
            return response()->json($orders, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to get orders'], 500);
        }
    }
    public function show($id)
    {
        try {
            $order = $this->orderRepository->findById($id);
            return response()->json($order, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to get order'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Crear una nueva orden",
     *     description="Crea una orden",
     *     operationId="createOrder",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "user_id","status", "total"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="total", type="number", example=50.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Orden creada",
     *         @OA\JsonContent(
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="total", type="number", example=50.0)
     *         )
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'product_id' => 'required|integer',
                'user_id' => 'required|integer',
                'status' => 'required|string|max:255',
                'total' => 'required|numeric',
            ], [
                'user_id.required' => 'El id del usuario es requerido.',
                'product_id.required' => 'El id del usuario es requerido.',
                'status.required' => 'El estado de la orden es requerido.',
                'total.required' => 'El precio total de la orden es obligatorio.',
                'total.numeric' => 'El precio total de la orden debe ser numérico.'
            ]);

            $order = $this->orderRepository->create($data);
            return response()->json($order, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'invalid order data', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to create order'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Actualizar una orden",
     *     description="Actualizar una orden por su ID",
     *     operationId="updateOrder",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             required={"status", "total"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="pending"),
     *             @OA\Property(property="total", type="number", example=50.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="orden updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada para actualizar",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Orden not found for update")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos para la orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="invalid data"),
     *             @OA\Property(property="details", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al actualizar la orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error to update order")
     *         )
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'user_id' => 'sometimes|integer',
                'product_id' => 'sometimes|integer',
                'status' => 'sometimes|string|max:255',
                'total' => 'sometimes|numeric',
            ], [
                'status.required' => 'El estado de la orden es requerido.',
                'total.required' => 'El precio total de la orden es obligatorio.',
                'total.numeric' => 'El precio total de la orden debe ser numérico.'
            ]);

            $order = $this->orderRepository->update($id, $data);
            return response()->json($order, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Orden no found for update'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'invalid data', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to update order'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Eliminar una orden",
     *     description="Elimina una orden por su ID",
     *     operationId="deleteOrder",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="deleted", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada para eliminar",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Orden not found for delete")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al eliminar la orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error to delete order")
     *         )
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */

    public function destroy($id)
    {
        try {
            $deleted = $this->orderRepository->delete($id);
            return response()->json(['deleted' => $deleted], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Orden not found for delete'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to delete order'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/export",
     *     tags={"Orders"},
     *     summary="Exportar órdenes",
     *     description="Exporta las órdenes a un archivo Excel",
     *     operationId="exportOrders",
     *     @OA\Response(
     *         response=200,
     *         description="Órdenes exportadas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="file", type="string", example="orders_export.xlsx")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al exportar las órdenes",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error to export orders")
     *         )
     *     ),
     *     security={{"sanctum": {}}}
     * )
     */

    public function exportOrders()
    {
        try {
            $excelService = ExcelExportService::getInstance();
            return $excelService->exportOrders(new OrdersExport);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to export orders'], 500);
        }
    }
}
