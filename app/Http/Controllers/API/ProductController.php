<?php

namespace App\Http\Controllers\API;

use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Services\ExcelExportService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Exception;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints para productos"
 * )
 */

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Listar todos los productos",
     *     description="Devuelve una lista de productos",
     *     operationId="getProducts",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function index()
    {
        try {
            $products = $this->productRepository->getAll();
            return response()->json($products, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error retrieving products'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Crear un nuevo producto",
     *     description="Crea un producto",
     *     operationId="createProduct",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "stock"},
     *             @OA\Property(property="name", type="string", example="Producto A"),
     *             @OA\Property(property="description", type="string", example="Descripción del producto"),
     *             @OA\Property(property="price", type="number", example=10.5),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Producto Name"),
     *             @OA\Property(property="description", type="string", example="Descripción del producto"),
     *             @OA\Property(property="price", type="number", example=10.5),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function show($id)
    {
        try {
            $product = $this->productRepository->findById($id);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }
            return response()->json($product, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error retrieving product'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
            ]);

            $product = $this->productRepository->create($data);
            return response()->json($product, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error creating product'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Actualizar un producto",
     *     description="Actualizar un producto por su ID",
     *     operationId="updateProduct",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *     required=false,
     *     @OA\JsonContent(
     *         required={"name", "price", "stock"},
     *         @OA\Property(property="name", type="string", example="Product Name"),
     *         @OA\Property(property="description", type="string", nullable=true, example="product description"),
     *         @OA\Property(property="price", type="number", format="float", example=50.0),
     *         @OA\Property(property="stock", type="integer", example=50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="product updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado para actualizar",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product not found for update")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos para el producto",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="invalid data"),
     *             @OA\Property(property="details", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al actualizar el producto",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error to update product")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|nullable|string',
                'price' => 'sometimes|numeric',
                'stock' => 'sometimes|integer',
            ], [
                'price.numeric' => 'El precio debe ser un número.',
                'stock.integer' => 'El stock debe ser un número entero.'
            ]);

            $product = $this->productRepository->update($id, $data);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($product, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product no found for update'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'invalid data', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to update product'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Eliminar un producto",
     *     description="Elimina un producto por su ID",
     *     operationId="deleteProduct",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="deleted", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Productos no encontrado para eliminar",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product not found for delete")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno al eliminar el producto",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error to delete product")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function destroy($id)
    {
        try {
            $deleted = $this->productRepository->delete($id);

            if (!$deleted) {
                return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['message' => 'Product deleted successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting product'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/products/export",
     *     tags={"Products"},
     *     summary="Exportar productos",
     *     description="Exporta los productos a un archivo Excel",
     *     operationId="exportProducts",
     *     @OA\Response(
     *         response=200,
     *         description="Productos exportados exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="file", type="string", example="products.xlsx")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al exportar los productos",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Error to export products")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function exportProducts()
    {
        try {
            $excelService = ExcelExportService::getInstance();
            return $excelService->exportProducts(new ProductsExport);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error to export products'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
