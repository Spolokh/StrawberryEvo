<div class="pagination pagination-centered">
    <ul>
    {{ BEGIN pages }}
        <li>
            <a class="{{ active }}" {{ IF link }}href="{{ link }}"{{ END }}>{{ page }}</a>
        </li>
    {{ END pages }}
    </ul>
</div>