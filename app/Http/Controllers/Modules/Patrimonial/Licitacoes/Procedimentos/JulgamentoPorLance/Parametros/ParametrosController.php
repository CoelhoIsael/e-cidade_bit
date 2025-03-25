<?php

namespace App\Http\Controllers\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\Parametros;

use App\Exceptions\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\JulgamentoException;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\Julgamento\ParametrosLancesRequest;
use App\Services\Patrimonial\Licitacao\Procedimentos\JulgamentoPorLance\FaseDeLancesService;
use Illuminate\Support\Facades\Session;

class ParametrosController extends Controller
{
    protected $julgamentoService;

    /**
     * Construtor do controller para inje��o de depend�ncia do JulgamentoService.
     *
     * @param JulgamentoService $julgamentoService
     */
    public function __construct(
        FaseDeLancesService $faseDeLancesService
    )
    {
        $this->faseDeLancesService = $faseDeLancesService;
    }

    /**
     * Exibe a p�gina principal de Parametros.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $param = $this->faseDeLancesService->obterParametros();

        if (!$param) {
            $param = [
                'l13_instit' => Session::get('DB_instit'),
                'l13_precoref' => [],
                'l13_difminlance' => number_format(0, 2, ',', '.'),
                'l13_clapercent' => number_format(0, 2, ',', '.')
            ];
        } else {
            $param['l13_difminlance'] = number_format($param['l13_difminlance'], 2, ',', '.');
            $param['l13_clapercent'] = number_format($param['l13_clapercent'], 2, ',', '.');
        }

        return view()->file(base_path('resources/legacy/licitacao/lic_fasedelance004.php'), ['param' => $param]);
    }

    /**
     * Atualiza os par�metros do julgamento.
     *
     * Esta fun��o valida os dados recebidos por meio do request,
     * chama o servi�o de julgamento para alterar os par�metros e
     * retorna uma resposta apropriada dependendo do resultado da opera��o.
     *
     * @param ParametrosLancesRequest $request Objeto contendo os par�metros validados da solicita��o.
     *
     * @return \Illuminate\Http\JsonResponse Retorna uma resposta JSON:
     * - Em caso de sucesso, uma mensagem indicando que os par�metros foram atualizados.
     * - Em caso de exce��o espec�fica de julgamento, um erro com c�digo 400 e a mensagem da exce��o.
     * - Em caso de erro inesperado, um erro gen�rico com c�digo 500.
     *
     * @throws JulgamentoException Lan�ada quando ocorre uma falha espec�fica na l�gica de julgamento.
     * @throws \Exception Lan�ada para erros inesperados n�o tratados especificamente.
     */
    public function update(ParametrosLancesRequest $request)
    {
        $validated = $request->validated();

        try {

            if ($validated['l13_clapercent'] == 0) {
                $validated['l13_clapercent'] = null;
            }

            $this->faseDeLancesService->alterarParametro($validated);

            return response()->json(['message' => StringHelper::toUtf8('Par�metros do julgamentos alterado com sucesso.')]);

        } catch (JulgamentoException $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        } catch (\Exception $e) {

            return response()->json(['error' => 'Erro inesperado.'], 500);

        }
    }
}
