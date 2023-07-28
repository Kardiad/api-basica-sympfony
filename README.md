# api-basica-sympfony
Esta api es para uso de aplicaciones genéricas en Front, y hacer pruebas, con temática de héroes.

# Endpoints 
        /heroe/nuevo : Te permite crear un nuevo héroe
                       Datos requeridos:    
                            -> nombre
                            -> alterego
                            -> codigo
                            -> aparicion
                            -> img 
        /heroe/listar: Te permite mostrar todos los héroes
        /heroe/modificar: Te permite modificar el héroe.
                        Datos requeridos:
                            -> id
                            -> nombre
                            -> alterego
                            -> codigo
                            -> aparicion
                            -> img 
        /heroe/borrar: Te permite borrar un héroe.
                        Datos requeridos:
                            -> id
        /usuario/nuevo: Crea un usuario
                        Datos requeridos:
                            -> usuario
                            -> contraseña
                            -> mail
        /usuario/modificar: Modifica los datos de usuario
                        Datos requeridos:
                            -> usuario
                            -> contraseña
                            -> mail
        /usuario/borrar: Borra un usuario
                        Datos requeridos:
                            -> usuario
                            -> contraseña
        /usuario/login: Obtiene el usuario
                        Datos requeridos:
                            -> usuario
                            -> contraseña 
