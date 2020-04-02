<?php
declare(strict_types=1);

namespace App\Validators\ArticleVersion;

use App\Contracts\Repositories\Wiki\IterationRepositoryContract;
use App\Models\Wiki\Iteration;
use App\Validators\BaseValidatorAbstract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Class SelectedIterationBelongsToArticleValidator
 * @package App\Validators\ArticleVersion
 */
class SelectedIterationBelongsToArticleValidator extends BaseValidatorAbstract
{
    /**
     * @var string
     */
    const KEY = 'selected_iteration_belongs_to_article';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var IterationRepositoryContract
     */
    private $iterationRepository;

    /**
     * SelectedIterationBelongsToArticleValidator constructor.
     * @param Request $request
     * @param IterationRepositoryContract $iterationRepository
     */
    public function __construct(Request $request, IterationRepositoryContract $iterationRepository)
    {
        $this->request = $request;
        $this->iterationRepository = $iterationRepository;
    }

    /**
     * This is invoked by the validator rule 'selected_iteration_belongs_to_article'
     *
     * @param $attribute string the attribute name that is validating
     * @param $value mixed the value that we're testing
     * @param $parameters array
     * @param $validator Validator The Validator instance
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        $this->ensureValidatorAttribute('iteration_id', $attribute);

        if (!$value) {
            return true;
        }

        $article = $this->request->route('article', null);

        if (!$article) {
            return false;
        }

        try {
            /** @var Iteration $iteration */
            $iteration = $this->iterationRepository->findOrFail($value);

            return $iteration->article_id == $article->id;

        } catch (ModelNotFoundException $e) {}

        return false;
    }
}