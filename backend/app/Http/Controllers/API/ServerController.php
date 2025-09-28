<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServerRequest;
use App\Http\Requests\UpdateServerOrderRequest;
use App\Http\Requests\UpdateServerRequest;
use App\Http\Resources\ServerResource;
use App\Models\Server;
use App\Services\ImageResizeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * Listar todos los servidores
     * @unauthenticated
     * @method GET
     * @url /api/servers
     * @response 200 {"data": [{"id":1,"host":"example.com","ip":"192.168.0 1","description":"Example server","image":null,"image_url":null,"order":1,"created_at":"2024-06-01 12:00:00","updated_at":"2024-06-01 12:00:00","deleted_at":null}],"count":1}  
     * @response 500 {"message": "Error interno del servidor."}
     * @return JsonResponse  
     */
    public function index(Request $request): JsonResponse
    {

        $servers = Server::orderBy('order' , 'ASC')->get();

        return response()->json([
            'data'  => ServerResource::collection($servers),
            'count' => $servers->count(),
        ], 200);
    }

    /**
     * Crear un servidor
     * @unauthenticated
     * @method POST
     * @url /api/servers
     * @bodyParam host string required El nombre del host. Example: telecentro.com
     * @bodyParam ip string required La dirección IP del servidor. Example: 192.12.12.1
     * @bodyParam description string La descripción del servidor. Example: Servidor de prueba
     * @response 200 {"data": {"id":1,"host":"example.com","ip":"192.168.0 1","description":"Example server","image":null,"image_url":null,"order":1,"created_at":"2024-06-01 12:00:00","updated_at":"2024-06-01 12:00:00","deleted_at":null}}  
     * @response 201 {"message": "Servidor creado correctamente.","data": {"id":1,"host":"telecentro.com","ip":"192.168.0.1","description":"Servidor de prueba","image":"servers/abcd1234.jpg","image_url":"http://localhost/storage/servers/abcd1234.jpg","order":1,"created_at":"2024-06-01 12:00:00","updated_at":"2024-06-01 12:00:00","deleted_at":null}}
     * @response 422 {"message": "The given data was invalid.","errors": {"ip": ["La IP debe tener formato IPv4 válido."]}}
     * @response 500 {"message": "Error interno del servidor."}     
     * @return JsonResponse  
     */
    public function store(StoreServerRequest $request, ImageResizeService $imageService): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $imageService->resizeAndStore($request->file('image'));
        }

        $server   = Server::create([
            'image'       => $data['image'] ?? null,
            'host'        => $data['host'],
            'ip'          => $data['ip'],
            'description' => $data['description'] ?? null
        ]);

        return response()->json([
            'message' => 'Servidor creado correctamente.',
            'data'    => new ServerResource($server),
        ], 201);
    }

    /**
     * Mostrar un server específico
     * @unauthenticated
     * @method GET
     * @url /api/servers/{server}
     * @bodyParam server int required El ID del servidor. Example: 1
     * @bodyParam host string required El nombre del host. Example: telecentro.com
     * @bodyParam ip string required La dirección IP del servidor. Example: 192.1.1.1
     * @bodyParam description string La descripción del servidor. Example: Servidor de prueba
     * @response 200 {"data": {"id":1,"host":"example.com","ip":"192.168.0 1","description":"Example server","image":null,"image_url":null,"order":1,"created_at":"2024-06-01 12:00:00","updated_at":"2024-06-01 12:00:00","deleted_at":null}}  
     * @response 404 {"message": "Servidor no encontrado."}  
     * @response 500 {"message": "Error interno del servidor."}     
     * @return JsonResponse
     */
    public function show(Server $server): JsonResponse
    {
        if (! $server) {
            return response()->json(['message' => 'Servidor no encontrado.'], 404);
        }

        return response()->json(['data' => new ServerResource($server)], 200);
    }

    /**
     * Modificar un servidor
     * @unauthenticated
     * @method PUT
     * @url /api/servers/{server}
     * @response 200 {"data": {"id":1,"host":"example.com","ip":"192.168.0 1","description":"Example server","image":null,"image_url":null,"order":1,"created_at":"2024-06-01 12:00:00","updated_at":"2024-06-01 12:00:00","deleted_at":null}}  
     * @response 404 {"message": "Servidor no encontrado."}  
     * @response 500 {"message": "Error interno del servidor."}
     * @return JsonResponse
     */
    public function update(UpdateServerRequest $request, Server $server, ImageResizeService $imageService): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if (!is_null($server->image))
                $imageService->delete($server->image);
            $server->image = $imageService->resizeAndStore($request->file('image'));
        }

        $server->host        = $data['host'];
        $server->ip          = $data['ip'];
        $server->description = $data['description'] ?? null;

        $server->save();

        return response()->json([
            'message' => 'Servidor actualizado correctamente.',
            'data'    => new ServerResource($server),
        ], 200);
    }

    /**
     * Eliminar un servidor
     * @unauthenticated
     * @method DELETE
     * @url /api/servers/{server}
     * @response 200 {"message": "Servidor eliminado (soft delete)."}
     * @response 404 {"message": "Servidor no encontrado."}
     * @response 500 {"message": "Error interno del servidor."}
     * @return JsonResponse
     */
    public function destroy(Server $server): JsonResponse
    {

        if (! $server) {
            return response()->json(['message' => 'Servidor no encontrado.'], 404);
        }

        $server->delete();

        return response()->json(['message' => 'Servidor eliminado.'], 200);
    }

    /**
     * Restaurar un servidor eliminado (soft delete)
     * @unauthenticated
     * @method POST
     * @url /api/servers/{server}/restore
     * @response 200 {"message": "Servidor restaurado correctamente.","data": {"id":1,"host":"example.com","ip":"192.168.0 1","description":"Example server","image":null,"image_url":null,"order":1,"created_at":"2024-06-01 12:00:00","updated_at":"2024-06-  01 12:00:00","deleted_at":null}}            
     * @response 400 {"message": "El servidor no está eliminado."}
     * @response 404 {"message": "Servidor no encontrado."}
     * @response 500 {"message": "Error interno del servidor."}
     * @return JsonResponse
     */
    public function restore(Server $server): JsonResponse
    {
        if (! $server) {
            return response()->json(['message' => 'Servidor no encontrado.'], 404);
        }

        if (! $server->trashed()) {
            return response()->json(['message' => 'El servidor no está eliminado.'], 400);
        }

        $server->restore();

        return response()->json(['message' => 'Servidor restaurado correctamente.', 'data' => new ServerResource($server)], 200);
    }

     /**
     * Cambiar el order de un servidor
     * @unauthenticated
     * @method PATCH
     * @url /api/servers/{server}/order
     * @response 200 {"message": "Orden actualizado.","data": {"id":1,"host":"example.com","ip":"192.168.0 1","description":"Example server","image":null,"image_url":null,"order":2,"created_at":"2024-06-01 12:00:00","updated_at":"2024-06-01 12:00:00","deleted_at":null}}  
     * @response 404 {"message": "Servidor no encontrado."}  
     * @response 500 {"message": "Error interno del servidor."}     
     * @return JsonResponse
     */
    public function updateOrder(UpdateServerOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        foreach ($data['servers'] as $item) {
            Server::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Orden actualizado correctamente.'], 200);
    }

}
