<h1>pacientes</h1>

<ul>
    <li><button>agregar</button></li>
    <li><button>quitar</button></li>
    <li><button>modificar</button></li>
</ul>

<!-- estas son pruebas de botones y tablas, aun no tiene funcionalidad
pero se pueden agregar funcionalidades internas con parametros atravez
de codigo interno-->
<style>
    table {
        border-collapse: collapse;
    }
    th,td {
        border: 1px solid black;
        padding: 3px;
    }
    li {
        display: inline;
        margin-right: 1rem;
    }
</style>

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
            <td><button>ver</button></td>
        </tr>
        <tr>
            <td>Jane Smith</td>
            <td>25</td>
            <td>Gripe</td>
            <td><button>ver</button></td>
        </tr>
    </tbody>
</table>