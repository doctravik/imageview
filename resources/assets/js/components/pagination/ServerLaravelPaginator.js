class ServerLaravelPaginator
{
    /**
     * Create a new server laravel paginator instance.
     * 
     * @param  object paginator
     * @return void
     */
    constructor(paginator) {
        this.paginator = paginator;
    }

    static make(paginator) {
        return (new ServerLaravelPaginator(paginator)).adapt();
    }

    adapt() {
        // console.log(this.paginator.current_page);
        return {
            'items': this.paginator.data,
            'currentPage': this.paginator.current_page,
            'lastPage': this.paginator.last_page,
            'perPage': this.paginator.per_page,
            'total': this.paginator.total
        };
    }
}

export default ServerLaravelPaginator;