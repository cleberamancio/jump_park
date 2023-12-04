<?php

namespace App\Http\Controllers\Api;

use App\Adapters\EloquentServiceOrderRepository;
use App\Core\CreateServiceOrderUseCase;
use App\Core\EditServiceOrderUseCase;
use App\Core\ListByIdServiceOrderUseCase;
use App\Core\ListByPlateServiceOrderUseCase;
use App\Core\ListPaginationOrderUseCase;
use App\Core\RemoveServiceOrderUseCase;
use App\Domain\ServiceOrder;
use App\Domain\ServiceOrder\ServiceOrderRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderPatchRequest;
use App\Http\Requests\OrderRequest;
use App\Utils\Messages;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *      title="Jump Park",
 *      version="1.0.0",
 *      description="API de demostração e avaliação",
 *      @OA\Contact(
 *          email="cleberamancio@gmail.com",
 *          name="Cleber Amâncio"
 *      ),
 *      @OA\License(
 *          name="MIT",
 *          url="https://byte1.com.br"
 *      )
 * )
 */
class ServiceOrderController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/service-order",
     *      summary="Listar ordens de serviço",
     *      description="Neste endpoint é possível listar e buscar ordens de serviços com paginação de 5 em 5 ítens.",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="vehiclePlate", type="string", maxLength=7),
     *              @OA\Property(property="start", type="integer", default=0),
     *              @OA\Property(property="limit", type="integer", default=5)
     *              
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Sucesso",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="vehiclePlate", type="string", maxLength=7),
     *                  @OA\Property(property="entryDateTime", type="string", format="date-time"),
     *                  @OA\Property(property="exitDateTime", type="string", format="date-time"),
     *                  @OA\Property(property="priceType", type="string"),
     *                  @OA\Property(property="price", type="string"),
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time"),
     *                  @OA\Property(property="user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="email", type="string")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Não autorizado",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      security={{"bearerAuth": {}}},
     * 
     * )
     * @OA\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      type="http",
     *      scheme="bearer",
     *      bearerFormat="JWT",
     * )
     **/
    public function index(Request $request)
    {
        $start = $request->input('start', 0);
        $limit = 5;
        $vehiclePlate = $request->input('vehiclePlate', "");
        $vehiclePlate = $vehiclePlate ?? '';

        $serviceOrderRepository = new ServiceOrderRepository(new EloquentServiceOrderRepository());
        $listPaginationOrderUseCase = new ListPaginationOrderUseCase($serviceOrderRepository);
        $return = $listPaginationOrderUseCase->execute($vehiclePlate, $start, $limit);

        return response()->json($return, 200);
    }
    /**
     * @OA\Post(
     *      path="/api/service-order",
     *      summary="Cria uma nova Ordem de Serviço",
     *      description="Neste endpoint é possível criar uma nova Ordem de Serviço.",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="vehiclePlate", type="string", maxLength=7, description="Placa do veículo", example="GSA2564"),
     *              @OA\Property(property="entryDateTime", type="string",  description="Horário de entrada", example="2023-11-29 16:00:00"),
     *              @OA\Property(property="exitDateTime", type="string", description="Horário de saída", example="2023-11-30 16:00:00"),
     *              @OA\Property(property="priceType", type="string", description="Tipo da Ordem de Serviço", example="Normal"),
     *              @OA\Property(property="price", type="float", description="Preço da Ordem de Serviço", example="30.60"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Sucesso",
     *          @OA\JsonContent(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="vehiclePlate", type="string", maxLength=7),
     *                  @OA\Property(property="entryDateTime", type="string", format="date-time"),
     *                  @OA\Property(property="exitDateTime", type="string", format="date-time"),
     *                  @OA\Property(property="priceType", type="string"),
     *                  @OA\Property(property="price", type="string"),
     *                  @OA\Property(property="user_id", type="integer"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time"),
     *                  @OA\Property(property="user", type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="email", type="string")
     *                  
     *              )
     *          )
     *      )
     * )
     **/
    public function store(OrderRequest $request)
    {
        $request->validated();

        $serviceOrderRepository = new ServiceOrderRepository(new EloquentServiceOrderRepository());
        $createServiceOrderUseCase = new CreateServiceOrderUseCase($serviceOrderRepository);
        
        $serviceOrder = new ServiceOrder();
        $serviceOrder->setVehiclePlate($request['vehiclePlate']);
        $serviceOrder->setEntryDateTime($request['entryDateTime']);
        if( !empty($request['exitDateTime']))
        {
            $serviceOrder->setExitDateTime($request['exitDateTime']);
        }
        $serviceOrder->setPriceType($request['priceType']);
        $serviceOrder->setPrice($request['price']);
        $serviceOrder->setUserId($request['userId']);
        return response()->json($createServiceOrderUseCase->execute($serviceOrder)->toArray(), 201);
    }

    /**
     * @OA\Post(
     *      path="/api/service-order/edit",
     *      summary="Editar uma Ordem de Serviço",
     *      description="Edita uma Ordem de Serviço ou um grupo de Ordens de Serviço enviando a placa do veíclo e o valor.",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="vehiclePlate", type="string", maxLength=7, description="Placa do veículo", example="GSA2564"),
     *              @OA\Property(property="entryDateTime", type="string",  description="Horário de entrada", example="2023-11-29 16:00:00"),
     *              @OA\Property(property="exitDateTime", type="string", description="Horário de saída", example="2023-11-30 16:00:00"),
     *              @OA\Property(property="priceType", type="string", description="Tipo da Ordem de Serviço", example="Normal"),
     *              @OA\Property(property="price", type="float", description="Preço da Ordem de Serviço", example="7.0")
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Sucesso",
     *          @OA\JsonContent(
     *                 @OA\Property(property="affected", type="integer", description="Quantidade de registros afetados")
     *          )
     *      )
     * )
     **/
    public function update(OrderPatchRequest $request)
    {
        $vehiclePlate = $request->input('vehiclePlate', 0);
        $price = $request->input('price', 0);

        $vehiclePlate = $vehiclePlate ?? 0;
        $price = $price ?? 0;

        $request->validated();

        $serviceOrder = new ServiceOrder();
        $serviceOrder->setVehiclePlate($request['vehiclePlate']);
        $serviceOrder->setEntryDateTime($request['entryDateTime']);
        $serviceOrder->setExitDateTime($request['exitDateTime']);
        $serviceOrder->setPriceType($request['priceType']);
        $serviceOrder->setPrice($request['price']);

        $serviceOrderRepository = new ServiceOrderRepository(new EloquentServiceOrderRepository());
        $listByPlateServiceOrderUseCase = new ListByPlateServiceOrderUseCase($serviceOrderRepository);
        $editServiceOrderUseCase = new EditServiceOrderUseCase($serviceOrderRepository,$listByPlateServiceOrderUseCase);
        $return = $editServiceOrderUseCase->execute($vehiclePlate, $price, $serviceOrder);
        return response()->json(array("affected"=>$return), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/service-order/delete",
     *      summary="Remove uma Ordem de Serviço",
     *      description="Remove uma Ordem de Serviço enviando o id.",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", description="Id da Ordem de Serviço na base de dados", example="1"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="204",
     *          description="No Content",
     *          @OA\JsonContent(
     *                 @OA\Property(property="status", type="boolean", description="True")
     *          )
     *      ),
    *      @OA\Response(
     *          response="500",
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *                 @OA\Property(property="message", type="string", description="Order not found!")
     *          )
     *      )
     * )
     **/
    public function destroy(Request $request)
    {
        $id = $request->input('id', 0);
        $id = $id ?? 0;
        if( !empty($id) )
        {
            $serviceOrderRepository = new ServiceOrderRepository(new EloquentServiceOrderRepository());
            $listByIdServiceOrderUseCase = new ListByIdServiceOrderUseCase($serviceOrderRepository);
            $removeServiceOrderUseCase = new RemoveServiceOrderUseCase($serviceOrderRepository, $listByIdServiceOrderUseCase);
            return response()->json(array("status" => $removeServiceOrderUseCase->execute($id)), 204);
        }
        return response()->json(Messages::$VEHICLE_NOT_FOUND, 404);
    }
}
