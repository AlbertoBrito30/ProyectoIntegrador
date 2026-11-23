<h1>pacientes</h1>

<ul>
    <li><button>agregar</button></li>
    <li><button>quitar</button></li>
    <li><button>modificar</button></li>
</ul>

<!-- estas son pruebas de botones y tablas, aun no tiene funcionalidad
pero se pueden agregar funcionalidades internas con parametros atravez
de codigo interno-->

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Diagnóstico</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>John Doe</td>
            <td>30</td>
            <td>Resfriado</td>
            <td>
                <button popovertarget="mas-1">ver</button>
                <div id="mas-1" popover>
                    <p>
                        datos de la mascota
                    </p>
                    <button popovertarget="mas-1" popovertargetaction='hide' >cerrar</button>
                </div>
            </td>
        </tr>
        <tr>
            <td>Jane Smith</td>
            <td>25</td>
            <td>Gripe</td>
            <td>
                <button popovertarget="mas-2">ver</button>

                <div id="mas-2" popover>
                    <p>
                        datos de la mascota
                    </p>
                    <button popovertarget="mas-2" popovertargetaction="hide">cerrrar</button>
                </div>

            </td>
        </tr>
    </tbody>
</table>