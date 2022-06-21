<?php
declare(strict_types=1);
namespace YuanxinHealthy\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Di\Annotation\Inject;
class BusinessLogMiddleware implements MiddlewareInterface
{

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!config('business_log.enable') || !config('business_log.rules')) {
            // 没启用.
            return $handler->handle($request);
        }
        $routeHand = $request->getAttribute(Dispatched::class)->handler;
        $method = strtoupper($request->getMethod() . '');
        $route = $routeHand->route . ':' . $method;
        $rules = config('business_log.rules');
        if (!isset($rules[$route])) {
            // 没有配置规则
            return $handler->handle($request);
        }
        $sign = empty($rules[$route]['sign']) ? $route : $rules[$route]['sign'];
        $requestInterface = \Hyperf\Utils\ApplicationContext::getContainer()->get(RequestInterface::class);
        $response = $handler->handle($request);
        if (isset($rules[$route]['callback'])) {
            $context = call_user_func_array($rules[$route]['callback'], [$requestInterface, $response, $route]);
        } else {
            $context = [
                'headers' => $request->getHeaders(),
                'request' => $requestInterface->all(),
                'response' => $response->getBody()->getContents(),
            ];
        }
        Log::get('business', 'businessLog')->info($sign, $context);
        return $response;
    }
}