<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatementStoreRequest;
use App\Models\Statement;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\Intl\Countries;


class StatementController extends Controller
{
    /**
     * @return View|Factory|Application
     */
    public function index(Request $request): View|Factory|Application
    {
        $statements = Statement::query();

        if ($request->get('automated_detection')) {
            $statements->whereIn('automated_detection', $request->get('automated_detection'));
        }

        if ($request->get('automated_takedown')) {
            $statements->whereIn('automated_takedown', $request->get('automated_takedown'));
        }

        if ($request->get('decision_ground')) {
            $statements->whereIn('decision_ground', $request->get('decision_ground'));
        }

        if ($request->get('platform_type')) {
            $statements->whereIn('platform_type', $request->get('platform_type'));
        }

        if ($request->get('created_at_start')) {
            $statements->where('created_at', '>=', Carbon::createFromFormat('d-m-Y', $request->get('created_at_start')));
        }

        if ($request->get('created_at_end')) {
            $statements->where('created_at', '<=', Carbon::createFromFormat('d-m-Y', $request->get('created_at_end')));
        }

        if ($request->get('countries_list')) {
            foreach ($request->get('countries_list') as $country) {
                $statements->where('countries_list', 'LIKE', '%"'.$country.'"%');
            }
        }

        if ($request->get('s')) {
            $statements->where('uuid', 'like', '%' . $request->get('s') . '%')->orWhereHas('user', function($query) use($request)
            {
                $query->where('name', 'LIKE', '%' . $request->get('s') . '%');
            });
        }

        $options = $this->prepareOptions();

        $total = $statements->count();

        $statements = $statements->orderBy('id', 'DESC')->paginate(50)->withQueryString();
        return view('statement.index', compact('statements', 'options', 'total'));
    }

    /**
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        $statement = new Statement();
        $statement->countries_list = [];

        $options = $this->prepareOptions();
        return view('statement.create', [
            'statement' => $statement,
            'options' => $options
        ]);
    }

    /**
     * @param Statement $statement
     *
     * @return Factory|View|Application
     */
    public function show(Statement $statement): Factory|View|Application
    {
        return view('statement.show', compact('statement'));
    }

    /**
     * @param StatementStoreRequest $request
     *
     * @return RedirectResponse
     */
    public function store(StatementStoreRequest $request): RedirectResponse
    {

        $validated = $request->safe()->merge([
            'user_id' => auth()->user()->id,
            'method' => Statement::METHOD_FORM
        ])->toArray();


//        $validated['date_sent'] = $this->sanitizeDate($validated['date_sent'] ?? null);
//        $validated['date_enacted'] = $this->sanitizeDate($validated['date_enacted'] ?? null);
        $validated['date_abolished'] = $this->sanitizeDate($validated['date_abolished'] ?? null);

        $statement = Statement::create($validated);

        $url = route('statement.show', [$statement]);
        return redirect()->route('statement.index')->with('success', 'The statement has been created: <a href="' . $url . '">' . $statement->title . '</a>');
    }

    private function sanitizeDate($date): ?string
    {
        return $date ? Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d 00:00:00') : null;
    }


    /**
     * @return array
     */
    private function prepareOptions(): array
    {
        // Prepare Options

        $european_countries_list = $this->getEuropean_countries_list();

        $countries = $this->mapForSelectWithKeys($european_countries_list);
        $automated_detections = $this->mapForSelectWithoutKeys(Statement::AUTOMATED_DETECTIONS);
        $automated_takedowns = $this->mapForSelectWithoutKeys(Statement::AUTOMATED_TAKEDOWNS);
        $platform_types = $this->mapForSelectWithKeys(Statement::PLATFORM_TYPES);

        array_map(function ($automated_detection) {
            return ['value' => $automated_detection, 'label' => $automated_detection];
        }, Statement::AUTOMATED_DETECTIONS);

        $decisions = $this->mapForSelectWithKeys(Statement::DECISIONS);
        $decision_grounds = $this->mapForSelectWithKeys(Statement::DECISION_GROUNDS);

        $illegal_content_fields = Statement::ILLEGAL_CONTENT_FIELDS;
        $incompatible_content_fields = Statement::INCOMPATIBLE_CONTENT_FIELDS;

        $sources = $this->mapForSelectWithKeys(Statement::SOURCES);
        $sources_other = Statement::SOURCE_OTHER;

        $redresses = $this->mapForSelectWithKeys(Statement::REDRESSES);

        return compact(
            'countries',
            'automated_detections',
            'automated_takedowns',
            'platform_types',
            'decisions',
            'decision_grounds',
            'illegal_content_fields',
            'incompatible_content_fields',
            'sources',
            'sources_other',
            'redresses'
        );
    }

    private function mapForSelectWithoutKeys($array)
    {
        return array_map(function ($value) {
            return ['value' => $value, 'label' => $value];
        }, $array);
    }

    private function mapForSelectWithKeys($array)
    {
        return array_map(function ($key, $value) {
            return ['value' => $key, 'label' => $value];
        }, array_keys($array), array_values($array));
    }

    /**
     * @return string[]
     */
    public function getEuropean_countries_list(): array
    {
        $european_country_codes = Statement::EUROPEAN_COUNTRY_CODES;

        $european_countries_list = array_filter(Countries::getNames(), function ($country_code) use ($european_country_codes) {
            return in_array($country_code, $european_country_codes);
        }, ARRAY_FILTER_USE_KEY);
        return $european_countries_list;
    }
}
