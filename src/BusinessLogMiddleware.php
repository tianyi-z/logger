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
        $response = $handler->handle($request);
        $routeHand = $request->getAttribute(Dispatched::class)->handler;
        $method = strtoupper($request->getMethod() . '');
        $route = $routeHand->route . ':' . $method;
        $rules = config('business_log.rules');
        if (!isset($rules[$route])) {
            // 没有配置规则
            return $response;
        }
        $sign = empty($rules[$route]['business_type']) ? $route : $rules[$route]['business_type'];
        $requestInterface = \Hyperf\Utils\ApplicationContext::getContainer()->get(RequestInterface::class);
        if (isset($rules[$route]['callback'])) {
            $context = call_user_func_array($rules[$route]['callback'], [$requestInterface, $response, $route]);
        } else {
            $context = [
                'headers' => $request->getHeaders(),
                'request' => $requestInterface->all(),
                'response' => $response->getBody()->getContents(),
            ];
            if (!empty($context['response']) && is_string($context['response'])) {
                $tmp = json_decode($context['response'], true);
                if (is_array($tmp)) {
                    $context['response'] = $tmp;
                }
            }
        }
        Log::get('business', 'businessLog')->info($sign, $context);
        return $response;
    }
}
